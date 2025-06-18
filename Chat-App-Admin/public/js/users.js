const searchbar = document.querySelector(".users .search input");
searchBtn = document.querySelector(".users .search button"),
usersList = document.querySelector(".users .users-list");


searchBtn.onclick = ()=>{
    searchbar.classList.toggle("active");
    searchbar.focus();
    searchBtn.classList.toggle("active");
    searchbar.value = "";
}


searchbar.onkeyup = ()=>{
    let searchTerm = searchbar.value;
    if (searchTerm !== "") {
        searchbar.classList.add("active");
    } else {
        searchbar.classList.remove("active");
    }
    
    //AJAX Code
   let xhr = new XMLHttpRequest(); //creating XML objects
   xhr.open("POST", "/Chat-App/php/search.php", true);

   xhr.onload = ()=>{
             if(xhr.readyState === XMLHttpRequest.DONE){
                if(xhr.status === 200){
                    let data = xhr.response;
                    usersList.innerHTML = data;
                   
                   }
             }
            }
   xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");       
   xhr.send("searchTerm=" +searchTerm);
}

setInterval(()=>{
 //AJAX Code
   let xhr = new XMLHttpRequest(); //creating XML objects
   xhr.open("GET", "/Chat-App/php/users.php", true);

   xhr.onload = ()=>{
             if(xhr.readyState === XMLHttpRequest.DONE){
                if(xhr.status === 200){
                    let data = xhr.response;
                    if(!searchbar.classList.contains("active")){ 
                        usersList.innerHTML = data;
                    }
                   }
             }
            }
   xhr.send();
},500);