const buttons = document.querySelectorAll("[data-carousell-button]")

buttons.forEach(button =>

{
    button.addEventListener("click", ()=>{

    
        const offset = button.dataset.carousellButton === "next" ? 1 : -1
       
            // FIXED: correct attribute selector
        const slider = button.closest("[data-carousell]");

        // FIXED: query slides from slider, NOT from button
        const slides = slider.querySelector("[data-slides]");


        const activeSlide= slides.querySelector("[data-active]")
        let newIndex = [...slides.children] .indexOf(activeSlide) + offset
        if (newIndex<0) newIndex = slides.children.length -1 
        if(newIndex >= slides.children.length) newIndex = 0 
    
        slides.children[newIndex]. dataset.active = true
            delete activeSlide.dataset.active
        })
 })

