
// preload picture before upload a receipt
let loadFile = (event) => {
    console.log("changed");
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};

let photo = document.querySelector("#photo");
photo.addEventListener("change", loadFile);