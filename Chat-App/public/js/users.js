const searchbar = document.querySelector(".users .search input");
const searchBtn = document.querySelector(".users .search button");
const usersList = document.querySelector(".users .users-list");

// Debug: Check if DOM elements are found
if (!searchbar || !searchBtn || !usersList) {
    console.error("DOM elements not found:", {
        searchbar: !!searchbar,
        searchBtn: !!searchBtn,
        usersList: !!usersList
    });
}

// Toggle search bar
if (searchBtn) {
    searchBtn.onclick = () => {
        searchbar.classList.toggle("active");
        searchbar.focus();
        searchBtn.classList.toggle("active");
        searchbar.value = "";
    };
}

// Search functionality
if (searchbar) {
    searchbar.onkeyup = () => {
        let searchTerm = searchbar.value;
        if (searchTerm !== "") {
            searchbar.classList.add("active");
        } else {
            searchbar.classList.remove("active");
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/Chat-App/php/search.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    let data = xhr.response;
                    // console.log("Search response:", data);
                    usersList.innerHTML = data;
                    attachUserClickHandlers();
                } else {
                    console.error("Search failed:", xhr.status, xhr.responseText);
                }
            }
        };
        xhr.onerror = () => console.error("Search request failed");
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("searchTerm=" + encodeURIComponent(searchTerm));
    };
}

// Periodic user list update
setInterval(() => {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "/Chat-App/php/users.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
                // console.log("Users response:", data);
                if (!searchbar || !searchbar.classList.contains("active")) {
                    usersList.innerHTML = data;
                    attachUserClickHandlers();
                }
            } else {
                // console.error("Users fetch failed:", xhr.status, xhr.responseText);
            }
        }
    };
    xhr.onerror = () => console.error("Users request failed");
    xhr.send();
}, 500);

// Attach click handlers to user links
function attachUserClickHandlers() {
    const userLinks = document.querySelectorAll(".users-list a");
    userLinks.forEach(link => {
        link.onclick = (e) => {
            e.preventDefault();
            const userId = link.getAttribute("href").split("user_id=")[1];
            loadUserChat(userId);
        };
    });
}