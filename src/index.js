const pageContent = document.getElementById("page-content");
const navMenu = document.getElementById("menu");
const navLinks = document.querySelectorAll("#menu nav a");
const navList = document.querySelector("#menu nav ul");
const navIndicator = document.getElementById("nav-indicator");
const defaultPage = "home.html";

const activeTextClass = "text-white";
const inactiveTextClass = "text-gray-700";

async function loadPage(url) {
  try {
    pageContent.innerHTML = '<div class="text-center p-20">Loading...</div>';
    const response = await fetch(url);
    if (!response.ok) {
      if (response.status === 404) {
        pageContent.innerHTML =
          '<div class="text-center p-20 text-red-600">Sorry, page not found.</div>';
        return;
      }
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const html = await response.text();
    pageContent.innerHTML = html;
    window.scrollTo(0, 0);
  } catch (error) {
    console.error("Error loading page:", error);
    pageContent.innerHTML =
      '<div class="text-center p-20 text-red-600">Failed to load page content. Please try again.</div>';
  }
}

async function handleReservationSubmit(event) {
  event.preventDefault();
  console.log("Reservation form submitted...");

  const form = event.target;
  const submitButton = form.querySelector('button[type="submit"]');
  const originalButtonText = submitButton.innerHTML;

  submitButton.disabled = true;
  submitButton.innerHTML = "Processing...";

  const formData = new FormData(form);

  try {
    const response = await fetch("process_reservation.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success && result.billing_details) {
      console.log("Server response (Success with Billing):", result);
      const details = result.billing_details;

      let billingHtml = `
              <div class="container mx-auto px-4 py-10 lg:py-16">
                  <h1 class="text-3xl md:text-4xl font-[PoppinsBold] text-center mb-8 text-gray-800">Billing Information</h1>
                  <div class="w-full lg:w-3/4 xl:w-2/3 mx-auto bg-white p-6 shadow-lg rounded-lg border border-gray-200 overflow-x-auto">
          `;

      if (details.calculation_error) {
        billingHtml += `<p class="text-red-500 text-center mb-4">${details.calculation_error}</p>`;
      }

      billingHtml += `
                      <p class="text-center text-green-600 mb-6">${
                        result.message || "Reservation Saved!"
                      }</p>
                      <table class="w-full border-collapse text-sm mb-6">
                          <thead>
                              <tr>
                                  <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left font-semibold text-gray-700">Field</th>
                                  <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left font-semibold text-gray-700">Value</th>
                              </tr>
                          </thead>
                          <tbody>
                              ${generateTableRow(
                                "Customer Name",
                                details.customer_name || "N/A"
                              )}
                              ${generateTableRow(
                                "Contact Number",
                                details.contact_number || "N/A"
                              )}
                              ${generateTableRow(
                                "From Date",
                                details.from_date || "N/A"
                              )}
                              ${generateTableRow(
                                "To Date",
                                details.to_date || "N/A"
                              )}
                              ${generateTableRow(
                                "Number of Days",
                                details.number_of_days !== null
                                  ? details.number_of_days
                                  : "N/A"
                              )}
                              ${generateTableRow(
                                "Room Type",
                                details.room_type || "N/A"
                              )}
                              ${generateTableRow(
                                "Room Capacity",
                                details.room_capacity || "N/A"
                              )}
                              ${generateTableRow(
                                "Payment Type",
                                details.payment_type || "N/A"
                              )}
                              ${generateTableRow(
                                "Rate Per Day",
                                formatCurrency(details.rate_per_day)
                              )}
                              ${generateTableRow(
                                "Subtotal (Before Charges)",
                                formatCurrency(details.subtotal)
                              )}
                              ${
                                details.discount_percent > 0
                                  ? generateTableRow(
                                      `Discount (${(
                                        details.discount_percent * 100
                                      ).toFixed(0)}%)`,
                                      `-${formatCurrency(
                                        details.discount_amount
                                      )}`
                                    )
                                  : ""
                              }
                              ${
                                details.additional_charge_percent > 0
                                  ? generateTableRow(
                                      `Additional Charge (${(
                                        details.additional_charge_percent * 100
                                      ).toFixed(0)}%)`,
                                      `+${formatCurrency(
                                        details.additional_charge_amount
                                      )}`
                                    )
                                  : ""
                              }
                              <tr class="bg-gray-200 font-bold">
                                  <td class="border border-gray-300 px-4 py-2 text-gray-900">Total Bill</td>
                                  <td class="border border-gray-300 px-4 py-2 text-gray-900">${formatCurrency(
                                    details.total_bill
                                  )}</td>
                              </tr>
                          </tbody>
                      </table>
                      <div class="text-center mt-8">
                         <a href="#home" data-page="home.html" class="page-link inline-block px-6 py-2 text-base font-semibold rounded-md shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-700 text-white hover:bg-blue-800">Back to Home</a>
                      </div>
                  </div>
              </div>
          `;

      pageContent.innerHTML = billingHtml;
    } else {
      console.error("Server error or missing details:", result);
      pageContent.innerHTML = `
              <div class="container mx-auto px-4 py-16 text-center">
                  <h2 class="text-2xl font-bold text-red-600 mb-4">Error!</h2>
                  <p class="text-lg text-gray-700">${
                    result.message ||
                    "Could not save reservation or retrieve billing details. Please try again."
                  }</p>
                   <p class="mt-6">
                      <a href="#reservation" data-page="reservation.html" class="page-link inline-block px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Try Again</a>
                  </p>
              </div>
          `;
    }
  } catch (error) {
    console.error("Form submission fetch/JSON error:", error);
    pageContent.innerHTML = `
           <div class="container mx-auto px-4 py-16 text-center">
               <h2 class="text-2xl font-bold text-red-600 mb-4">Network/Response Error!</h2>
               <p class="text-lg text-gray-700">Could not process the request. Please check connection or contact support.</p>
               <p class="mt-6">
                   <a href="#reservation" data-page="reservation.html" class="page-link inline-block px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Try Again</a>
               </p>
           </div>
          `;
  }
}

if (pageContent) {
  console.log("Attaching submit listener to #page-content"); // Log listener attachment
  pageContent.addEventListener("submit", (event) => {
    console.log(
      "Submit event detected on #page-content. Target:",
      event.target
    );

    if (event.target.matches("#reservation-form")) {
      console.log(
        "Submit event listener matched #reservation-form. Calling handleReservationSubmit..."
      );
      handleReservationSubmit(event);
    } else {
      console.log(
        "Submit event ignored (target did not match #reservation-form)"
      );
    }
  });
} else {
  console.error(
    "#page-content element not found. Cannot attach submit listener."
  );
}

function formatCurrency(amount) {
  if (amount === null || amount === undefined) return "N/A";

  return "$" + parseFloat(amount).toFixed(2);
}

function generateTableRow(label, value) {
  return `
      <tr class="odd:bg-white even:bg-gray-50">
          <td class="border border-gray-300 px-4 py-2 text-gray-800 font-medium">${label}</td>
          <td class="border border-gray-300 px-4 py-2 text-gray-800">${value}</td>
      </tr>
  `;
}

function moveIndicator(activeLink) {
  if (!navList || !navIndicator) {
    console.error("Navigation list or indicator not found.");
    return;
  }

  navLinks.forEach((link) => {
    link.classList.remove(activeTextClass);
    if (!link.classList.contains(inactiveTextClass)) {
      link.classList.add(inactiveTextClass);
    }
  });

  if (activeLink) {
    activeLink.classList.add(activeTextClass);
    activeLink.classList.remove(inactiveTextClass);

    const activeLi = activeLink.closest("li");

    if (activeLi) {
      const targetLeft = activeLi.offsetLeft;
      const targetTop = activeLi.offsetTop;
      const targetWidth = activeLi.offsetWidth;
      const targetHeight = activeLi.offsetHeight;

      navIndicator.style.left = `${targetLeft}px`;
      navIndicator.style.width = `${targetWidth}px`;
      navIndicator.style.top = `${targetTop}px`;
      navIndicator.style.height = `${targetHeight}px`; // Ensure height is set
    } else {
      navIndicator.style.width = "0px";
      console.warn("Active LI parent not found for", activeLink);
    }
  } else {
    navIndicator.style.width = "0px";
    console.log("No active link provided, hiding indicator.");
  }
}

function handleNavClick(event) {
  const link = event.target.closest("a");
  if (!link || !link.matches("#menu nav a")) return;

  event.preventDefault();

  const pageUrl = link.dataset.page;
  if (pageUrl) {
    loadPage(pageUrl);
    moveIndicator(link);
    if (location.hash !== link.hash) {
      history.pushState({ page: pageUrl }, "", link.hash);
    }
  }
}

if (navMenu) {
  navMenu.addEventListener("click", handleNavClick);
} else {
  console.error("Nav menu element not found");
}

function initializePage() {
  let initialPageUrl = defaultPage;
  let currentHash = window.location.hash || "#home";
  let initialLink = document.querySelector(`nav a[href="${currentHash}"]`);

  if (initialLink && initialLink.dataset.page) {
    initialPageUrl = initialLink.dataset.page;
  } else {
    initialPageUrl = defaultPage;
    initialLink = document.querySelector(`nav a[href="#home"]`);
  }

  loadPage(initialPageUrl).then(() => {
    setTimeout(() => {
      if (initialLink) {
        moveIndicator(initialLink);
      } else if (navLinks.length > 0) {
        moveIndicator(navLinks[0]);
      } else {
        moveIndicator(null);
      }
    }, 0);
  });
}

window.addEventListener("popstate", (event) => {
  console.log("popstate triggered", window.location.hash, event.state);
  let currentHash = window.location.hash || "#home";
  let linkFromHash = document.querySelector(`nav a[href="${currentHash}"]`);
  let pageUrl = defaultPage;

  if (linkFromHash && linkFromHash.dataset.page) {
    pageUrl = linkFromHash.dataset.page;
  } else {
    linkFromHash = document.querySelector(`nav a[href="#home"]`);
    pageUrl = defaultPage;
  }

  loadPage(pageUrl).then(() => {
    setTimeout(() => {
      moveIndicator(linkFromHash);
    }, 0);
  });
});

document.addEventListener("DOMContentLoaded", initializePage);
