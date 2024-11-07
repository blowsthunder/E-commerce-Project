
//Scroll Bar and hidden part
document.addEventListener("DOMContentLoaded", function() {

        // Scroll behavior code
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('nav_bar');
        const scrollY = window.scrollY;

        if (scrollY > 300) { // Change 100 to the desired scroll threshold
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });


    // Dropdown menu code
    const dropdownIcon = document.getElementById("dropdown-icon");
    const dropdownMenu = document.getElementById("dropdown-menu");

    if(dropdownIcon && dropdownMenu){
    dropdownIcon.addEventListener("click", function() {
        dropdownMenu.classList.toggle("hidden");
        dropdownIcon.classList.toggle("rotate");
    })};

    //Pannel
    const cardIcon = document.getElementById('pannel');
    const blur = document.querySelector('.card_pannel_screen_blur');
    const cardPanel = document.getElementById('card_pannel');
    
    cardIcon.addEventListener('click', () => {
        blur.classList.add('active');
    });
    
    cardPanel.addEventListener('click', (event) => {
        // Prevent the click event from bubbling up to the parent elements
        event.stopPropagation();
    });
    
    blur.addEventListener('click', () => {
        blur.classList.remove('active');
    });

    function DeleteItem(id) {
        const confirmbutton = document.getElementById('confirm');
        const blur = document.getElementById('blur');
        const yesButton = document.getElementById('yes');
        const noButton = document.getElementById('non');

        confirmbutton.classList.add('active');
        blur.addEventListener('click', () => {
            confirmbutton.classList.remove('active');
        });

        yesButton.addEventListener('click', () => {
            updateCartItem(id, 'delete');
            confirmbutton.classList.remove('active');
            document.getElementById('deleteBtn-' + id).remove;
        });

        noButton.addEventListener('click', () => {
            confirmbutton.classList.remove('active');
        })


    }



//     // Image Changing and catcher changing
//     const images = ["bg1.jpg", "bg2.jpg"]; // List of image filenames
//     const imgElement = document.querySelector('.main img');
//     const h1Element = document.getElementById('catcher-h1');
//     const h4Element = document.getElementById('catcher-h4');
//     const bgImage = document.getElementById('background_image');
//     let currentImageIndex = 0;
//     let shouldRestartInterval = true; // Flag to control interval restart
    
//     const contentData = [
//         { h1: "Unleash the Demon Within", h4: "Forged in the Gym, Worn with Stoic Pride" },
//         { h1: "the Right Clothes", h4: "Begin Your Journey to Success with" },
//         // Add more data as needed
//     ];
    
//     let isAnimating = false; // Flag to prevent concurrent animations

// function rotateContent() {
//     if (isAnimating) {
//         return; // Don't trigger animation if it's already running
//     }

//     isAnimating = true; // Set flag to indicate animation is running
//     const currentData = contentData[currentImageIndex % contentData.length];
//     h1Element.style.opacity = 0;
//     h4Element.style.opacity = 0;
//     imgElement.style.opacity =0;
//     imgElement.style.transform = 'translateY(-300px)';


//     setTimeout(() => {
//         imgElement.src = "assets/images/" + images[currentImageIndex % images.length];
//         imgElement.style.opacity = 1;
//         imgElement.style.transform = "translateY(0px)";

//         h1Element.textContent = currentData.h1;
//         h4Element.textContent = currentData.h4;
//         h1Element.style.transform = "translateX(-200px)";
//         h4Element.style.transform = "translateX(-200px)";

//         setTimeout(() => {
//             h1Element.style.opacity = 1;
//             h1Element.style.transform = "translateX(0px)";
//             h4Element.style.opacity = 1;
//             h4Element.style.transform = "translateX(0px)";

//             isAnimating = false; // Reset flag after animation completes
//         }, 600);

//     }, 200);

//     currentImageIndex++;

//     if (shouldRestartInterval) {
//         clearInterval(rotationInterval);
//         rotationInterval = setInterval(rotateContent, 5000);
//     }
// }
    
//     // Call the rotateContent function every 5 seconds
//     let rotationInterval = setInterval(rotateContent, 5000); // 5000 milliseconds = 5 seconds
    
//     bgImage.addEventListener('click', () => {
//         shouldRestartInterval = false; // Don't restart interval immediately
//         rotateContent();
//         shouldRestartInterval = true; // Allow interval restart after this click
//     });



//     //Question et reponse 
//     const questions = document.querySelectorAll('.Question');

// questions.forEach(question => {
//     question.addEventListener('click', () => {
//         const answer = question.nextElementSibling;
//         const arrow = question.querySelector('.fa-arrow-up-long');

//         answer.classList.toggle('show');
//         arrow.style.transform = answer.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
//     });
// });

    
    

});



