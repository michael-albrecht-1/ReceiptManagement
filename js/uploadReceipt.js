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

// si on sélectionne un autre type on appelle la fonction "handleSelectChange"
let selectNode = document.querySelector("#type");
selectNode.addEventListener("change", (event) => {
    handleSelectChange(event.target.value);
});


// select the right TVA code on when DOM is loaded 
window.addEventListener("DOMContentLoaded", () => {
    const queryString = window.location.search; // cherche si on recoit des apramètres dans l'URL
    if (queryString == "") {
        optionNodes = document.querySelectorAll("option");
        optionNodes.forEach(element => {
            if (element.selected === true) {
                handleSelectChange(element.value);
            }
        });
    } else {
        const urlParams = new URLSearchParams(queryString);
        const tva = urlParams.get('tva');
    
        console.log("on essaye de récup le taux de tva en update de ticket");
        let tva1Input = document.querySelector("#tva1");
        let tva2Input = document.querySelector("#tva2");
        let tva3Input = document.querySelector("#tva3");
        let tva4Input = document.querySelector("#tva4");
        console.log("param : " + tva);
    
        let idTva;
        switch (tva) {
            case "0":
                idTva = "tva1";
                break;
            case "5.5":
                idTva = "tva2";
                break;
            case "10":
                idTva = "tva3";
                break;
            case "20":
                idTva = "tva4";
                break;
            default:
                idTva = "tva4"
                break;
        }
      
        if (tva1Input.id  == idTva) {
            tva1Input.checked = true;
        }   else {
            tva1Input.checked = false;
        }
    
        if (tva2Input.id  == idTva) {
            tva2Input.checked = true;
        }   else {
            tva2Input.checked = false;
        }
    
        if (tva3Input.id  == idTva) {
            tva3Input.checked = true;
        }   else {
            tva3Input.checked = false;
        }
    
        if (tva4Input.id  == idTva) {
            tva4Input.checked = true;
        }   else {
            tva4Input.checked = false;
        }
    }
})



    