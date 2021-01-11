let loadFile = function(event) {
    console.log("changed");
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};