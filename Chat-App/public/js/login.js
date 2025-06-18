const form = document.querySelector(".login form"),
continurBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-txt");


form.onsubmit = (e)=>{
e.preventDefault(); //prevent from form submit
}


continurBtn.onclick = ()=>{
   //AJAX Code
   let xhr = new XMLHttpRequest(); //creating XML objects
   xhr.open("POST", "../php/login.php",true);
   xhr.onload = ()=>{
             if(xhr.readyState === XMLHttpRequest.DONE){
                if(xhr.status === 200){
                    let data = xhr.response;
                    console.log(data);
                    if(data == "success"){
                        location.href = "../public/users.php";

                    }else{
                        errorText.textContent = data;
                        errorText.style.display = "block";
                        

                    }
                    
                }
             }
   } // we have to send data throigh ajax to php
   let formData = new FormData(form); //new formdata object
   xhr.send(formData); //sending the form data to php
}