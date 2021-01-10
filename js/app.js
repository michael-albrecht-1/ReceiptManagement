let loadFile = function(event) {
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};