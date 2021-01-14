// APP for the update/upload receipt page
//----------------------------------------

// preload picture before upload a receipt
let preloadReceiptPhoto = (event) => {
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};



window.addEventListener("DOMContentLoaded", () => {
    tva.init();
})



    