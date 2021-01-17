
let filter = {
    init: () => {
        filter.loadIsCheckedCheckbox();
    },

    loadIsCheckedCheckbox: () => {
        
        // extract from the cookies the checkbox value
        let cookieCheckbox;
        document.cookie.split("; ").forEach( e => {
            if (e.startsWith('isCheckedJS')){
                cookieCheckbox = e;
            }
        })
        let result = cookieCheckbox.split('=')[1];


        // update the DOM with it
        const checkboxNodes = document.querySelectorAll('#filter-form input');
        checkboxNodes.forEach(element => {
            if (element.value == result) {
                element.checked = true;
            }
        });

    }
}



window.addEventListener("DOMContentLoaded", () => {
	filter.init();
})
