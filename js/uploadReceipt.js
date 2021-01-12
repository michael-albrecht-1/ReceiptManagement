// preload picture before upload a receipt
let loadFile = (event) => {
    console.log("changed");
	let image = document.getElementById('preload');
	image.src = URL.createObjectURL(event.target.files[0]);
};

let photo = document.querySelector("#photo");
photo.addEventListener("change", loadFile);



// update TVA when a type of recept is selected ! EN TRAVAUX ! 
let handleSelectChange = (selectedType) => {
    
    let tva1Node = document.querySelector("#tva1");
    let tva2Node = document.querySelector("#tva2");
    let tva3Node = document.querySelector("#tva3");
    let tva4Node = document.querySelector("#tva4");
    switch (selectedType) {
        case "0":
            tva1Node.checked = false;
            tva2Node.checked = false;
            tva3Node.checked = true;
            tva4Node.checked = false;
            break;
        case "1":
            tva1Node.checked = false;
            tva2Node.checked = false;
            tva3Node.checked = false;
            tva4Node.checked = true;
            break;
        case "2":
            tva1Node.checked = false;
            tva2Node.checked = false;
            tva3Node.checked = false;
            tva4Node.checked = true;
            break;
        case "3":
            tva1Node.checked = true;
            tva2Node.checked = false;
            tva3Node.checked = false;
            tva4Node.checked = false;
            break;
        case "4":
            tva1Node.checked = false;
            tva2Node.checked = false;
            tva3Node.checked = false;
            tva4Node.checked = true;
            break;
        default:
            break;
    }

}

let selectNode = document.querySelector("#type");
selectNode.addEventListener("change", (event) => {
    handleSelectChange(event.target.value);
});


// select the right TVA code on when DOM is loaded 
window.addEventListener("DOMContentLoaded", () => {
    optionNodes = document.querySelectorAll("option");
    optionNodes.forEach(element => {
        if (element.selected === true) {
            handleSelectChange(element.value);
        }
    });
    // handleSelectChange();
})