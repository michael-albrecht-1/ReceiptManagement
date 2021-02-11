
let filter = {
    init: () => {
        filter.loadIsCheckedCheckbox();
    },

    // extract the isChecked value from the cookie and update the DOM
    loadIsCheckedCheckbox: () => {
        
        //! extract from the cookies - > TO FIX WE DONT USE COKKIES ANYMORE
        /*
        let cookieCheckbox;
        document.cookie.split("; ").forEach( e => {
            if (e.startsWith('isCheckedJS')){
                cookieCheckbox = e;
            }
        })
        let result = cookieCheckbox.split('=')[1];


        // update the DOM
        const checkboxNodes = document.querySelectorAll('#filter-form input');
        checkboxNodes.forEach(element => {
            if (element.value == result) {
                element.checked = true;
            }
        });
        */
    }
}



window.addEventListener("DOMContentLoaded", () => {
	filter.init();
})
