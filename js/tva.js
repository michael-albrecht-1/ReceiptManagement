let tva = {
    selectedCategory: null,
    init: () => {
        tva.getTVA();
        document.querySelector("#receiptCategory").addEventListener("change", (event) => tva.handleSelectCategory(event.target.value));
    },

    // update TVA when a type of receipt is selected
    handleSelectCategory: () => {
        
        console.log("ds");
        optionNodes = document.querySelectorAll("option");
            optionNodes.forEach(element => {
                if (element.selected === true) {
                    tva.selectedCategory = element.value;
                }
            })
        
        let tva1Node = document.querySelector("#tva1");
        let tva2Node = document.querySelector("#tva2");
        let tva3Node = document.querySelector("#tva3");
        let tva4Node = document.querySelector("#tva4");

        switch (tva.selectedCategory) {
            case "0":
                tva3Node.checked = true;
                return;
            case "1":
                tva4Node.checked = true;
                return;
            case "2":
                tva4Node.checked = true;
                return;
            case "3":
                tva1Node.checked = true;
                return;
            case "4":
                tva4Node.checked = true;
                return;
            default:
                return;
        }

    },

    getTVA: () => {
        const queryString = window.location.search; // GET params from URL
        const urlParams = new URLSearchParams(queryString);
        const tvaParam = urlParams.get('tva');
        if (tvaParam == null) { // NEW RECEIPT CASE
            tva.handleSelectCategory();
        } else { // UPDATE RECEIPT CASE
            let tva1Input = document.querySelector("#tva1");
            let tva2Input = document.querySelector("#tva2");
            let tva3Input = document.querySelector("#tva3");
            let tva4Input = document.querySelector("#tva4");
            console.log("param : " + tva);
        
            let idTva;
            switch (tvaParam) {
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
    }
}