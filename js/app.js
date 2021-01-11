
// preload picture before upload a receipt
let loadFile = (event) => {
    console.log("changed");
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};

let photo = document.querySelector("#photo");
photo.addEventListener("change", loadFile);


// update TVA when a type of recept is selected ! EN TRAVAUX ! 
let handleSelectChange = (event) => {
    
    let tvaNode1 = document.querySelector("#tva1");
    let tvaNode2 = document.querySelector("#tva2");
    let tvaNode3 = document.querySelector("#tva3");
    let tvaNode3 = document.querySelector("#tva3");
    
    switch (event.target.value) {
        case 0:
            tvaNode1.checked = false;
            tvaNode2.checked = false;
            tvaNode3.checked = true;
            tvaNode4.checked = false;
            break;
        case 1:
            tvaNode1.checked = false;
            tvaNode2.checked = false;
            tvaNode3.checked = false;
            tvaNode4.checked = true;
            break;
        case 2:
            tvaNode1.checked = false;
            tvaNode2.checked = false;
            tvaNode3.checked = false;
            tvaNode4.checked = true;
            break;
        case 3:
            tvaNode1.checked = true;
            tvaNode2.checked = false;
            tvaNode3.checked = false;
            tvaNode4.checked = false;
            break;
        case 4:
            tvaNode1.checked = false;
            tvaNode2.checked = false;
            tvaNode3.checked = false;
            tvaNode4.checked = true;
            break;
        default:
            break;
    }

}

let selectNode = document.querySelector("#type");
selectNode.addEventListener("change", handleSelectChange);