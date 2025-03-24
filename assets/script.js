document.addEventListener("DOMContentLoaded", function () {
    // Toggle navigation menu on mobile
    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");

    menuToggle.addEventListener("click", function () {
        navLinks.classList.toggle("active");
    });

    // Fetch Climate Data
    window.fetchClimateData = function () {
        const county = document.getElementById("county").value;
        const climateInfo = document.getElementById("climate-info");

        if (!county) {
            climateInfo.innerHTML = "<p style='color: red;'>Please select a county.</p>";
            return;
        }

        climateInfo.innerHTML = "<p>Fetching climate data for " + county + "...</p>";
        
        // Simulated API response (Replace with real API call)
        setTimeout(() => {
            climateInfo.innerHTML = `
                <h3>Climate Data for ${county}</h3>
                <p><strong>Temperature:</strong> 25°C</p>
                <p><strong>Rainfall:</strong> Moderate</p>
                <p><strong>Recommended Crops:</strong> Maize, Beans, Tomatoes</p>
            `;
        }, 1000);
    };

    // Fetch Irrigation Recommendations
    window.fetchIrrigationData = function () {
        const countyType = document.getElementById("irrigation-county").value;
        const waterSource = document.getElementById("water-source").value;
        const irrigationInfo = document.getElementById("irrigation-info");

        if (!countyType || !waterSource) {
            irrigationInfo.innerHTML = "<p style='color: red;'>Please select county type and water source.</p>";
            return;
        }

        irrigationInfo.innerHTML = "<p>Fetching irrigation recommendations...</p>";
        
        // Simulated API response (Replace with real API call)
        setTimeout(() => {
            irrigationInfo.innerHTML = `
                <h3>Irrigation Plan</h3>
                <p><strong>County Type:</strong> ${countyType}</p>
                <p><strong>Water Source:</strong> ${waterSource}</p>
                <p><strong>Recommended Method:</strong> Drip Irrigation</p>
                <p><strong>Water Usage Efficiency:</strong> High</p>
            `;
        }, 1000);
    };
});
