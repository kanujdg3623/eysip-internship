<script>
const data = [
    {
        name: "e-Yantra Robotics Competition",
        shortName:"eYRC",
        imageSet: [
            {
                imageCode: "rr",
                images:[
                "{{ asset('img/artwork/rr/rr7x5_1.png') }}",
                "{{ asset('img/artwork/rr/rr7x5_2.png') }}",
                "{{ asset('img/artwork/rr/rr7x5_3.png') }}",
                "{{ asset('img/artwork/rr/rr7x5_4.png') }}"
                ]
            }
        ]
    }, {
        name: "e-Yantra Ideas Competition",
        shortName:"eYIC",
        imageSet: [
            {
                imageCode: "bp",
                images:[
                "{{ asset('img/artwork/bp/bp7x5_1.png') }}",
                "{{ asset('img/artwork/bp/bp7x5_2.png') }}",
                "{{ asset('img/artwork/bp/bp7x5_3.png') }}",
                "{{ asset('img/artwork/bp/bp7x5_4.png') }}"
                ]
            }
        ]
    }, {
        name: "e-Yantra Lab Setup Initiative",
        shortName:"eLSI",
        imageSet: [
            {
                imageCode: "cb",
                images:[
                "{{ asset('img/artwork/cb/cb7x5_1.png') }}",
                "{{ asset('img/artwork/cb/cb7x5_2.png') }}",
                "{{ asset('img/artwork/cb/cb7x5_3.png') }}",
                "{{ asset('img/artwork/cb/cb7x5_4.png') }}"
                ]
            }
        ]
    }, {
        name: "e-Yantra Summer Internship Programme",
        shortName:"eYSIP",
        imageSet: [
            {
                imageCode: "pf",
                images:[
                "{{ asset('img/artwork/pf/pf7x5_1.png') }}",
                "{{ asset('img/artwork/pf/pf7x5_2.png') }}",
                "{{ asset('img/artwork/pf/pf7x5_3.png') }}"
                ]
            }
        ]
    }, {
        name: "e-Yantra Farm Setup Initiative",
        shortName:"eFSI",
        imageSet: [
            {
                imageCode: "sb",
                images:[
                "{{ asset('img/artwork/sb/sb7x5_1.png') }}",
                "{{ asset('img/artwork/sb/sb7x5_2.png') }}",
                "{{ asset('img/artwork/sb/sb7x5_3.png') }}"
                ]
            }
        ]
    }, {
        name: "e-Yantra Workshops",
        shortName:"eYW",
        imageSet: [
            {
                imageCode: "sr",
                images:[
                "{{ asset('img/artwork/sr/sr7x5_1.png') }}",
                "{{ asset('img/artwork/sr/sr7x5_2.png') }}",
                "{{ asset('img/artwork/sr/sr7x5_3.png') }}",
                "{{ asset('img/artwork/sr/sr7x5_4.png') }}"
                ]
            }
        ]
    }, 
]
let initiativeElement = document.getElementById("initiatives-home-content")
let currentIndex = 0;
function renderCardImage(imageData){
    let imageRender = "";
    for (let index = 0; index < imageData["images"].length; index++) {
        const element = `
            <div class="item-${index}">
                <figure class="image is-4by3">
                    <img src="${imageData["images"][index]}" alt="Picture-${imageData["imageCode"]}-${index}">
                </figure>
            </div>
            `
        imageRender += element
    }
    return (
        `<div class="carousel-images" style="pointer-events:none">
            ${imageRender}
        </div>
        `
    )
    
}
function setChild(index, breakpoint) {
    let element = "";
    for (let i = 0; i < breakpoint; i++) {
        const elementData = data[(i+index)%data.length];
        const randomImageSetter = elementData["imageSet"][Math.floor(Math.random() * elementData["imageSet"].length)]
        const elementRender = `
            <div class="column is-half-tablet is-one-quarter-desktop fade">
                <div class="card">
                    <div class="card-image">
                        ${renderCardImage(randomImageSetter)}
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <p class="title is-4">${elementData["name"]}</p>
                                <p class="subtitle is-6">${elementData["shortName"]}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
        element += elementRender;
    }
    return element;
}
function setParent(index, breakpoint) {
    const child = setChild(index, breakpoint);
    return `<div class="columns is-centered">${child}</div>`
}
// function setPagination(numOfPages, index, breakpoint){
//     console.log(numOfPages, index, breakpoint)
//     let innerSlider = ""
//     for (let index = 0; index < numOfPages; index++) {
//         innerSlider += `<div class="slider-page is-active" data-index="${index}"></div>`
//     }
//     let innerHTML = `
//     <div class="slider-pagination">
//         ${innerSlider}
//     </div>`
//     return innerHTML
// }
function setNavigation(breakpoint) {
    return `<div class="slider-navigation-previous" onclick="contentSetter(${currentIndex-breakpoint}, ${breakpoint})">
        <svg viewBox="0 0 50 80" xml:space="preserve">
            <polyline fill="currentColor" stroke-width=".5em" stroke-linecap="round" stroke-linejoin="round" points="45.63,75.8 0.375,38.087 45.63,0.375 "></polyline>
        </svg>
    </div>
    <div class="slider-navigation-next" onclick="contentSetter(${currentIndex+breakpoint}, ${breakpoint})">
        <svg viewBox="0 0 50 80" xml:space="preserve">
            <polyline fill="currentColor" stroke-width=".5em" stroke-linecap="round" stroke-linejoin="round" points="0.375,0.375 45.63,38.087 0.375,75.8 "></polyline>
        </svg>
    </div>`
}
function contentSetter(index, breakpoint){
    currentIndex = index%data.length;
    const pagination = null; //setPagination(Math.ceil(data.length / breakpoint), currentIndex, breakpoint)
    const navigation = setNavigation(breakpoint)
    const mainCardElement = setParent(currentIndex,breakpoint)
    initiativeElement.innerHTML = `${mainCardElement}${navigation}${pagination?"":""}`
    const imgCarousels = bulmaCarousel.attach('.carousel-images', {
        slidesToScroll: 1,
        slidesToShow: 1,
        navigation:false,
        navigationKeys:false,
        loop:true,
        infinite: true,
        duration: 3000,
        timing: "linear",
        autoplay:true,
        autoplaySpeed:500,
        pauseOnHover:false,
        breakpoints: [{ changePoint: 480, slidesToShow: 1, slidesToScroll: 1 }, { changePoint: 640, slidesToShow: 1, slidesToScroll: 1 }, { changePoint: 768, slidesToShow: 1, slidesToScroll: 1 } ]
    });
    imgCarousels.forEach(element => {
        const superParentNode = element.element.parentNode.parentNode;
        element.stop()
        superParentNode.addEventListener('mouseenter', e => {
            element.start()
        });
        superParentNode.addEventListener('mouseleave', e => {
            element.stop()
        });
    });
}
function breakpointSetter(currentIndex){
    const windowWidth = window.innerWidth;
    if(windowWidth < 769){
        contentSetter(currentIndex,1)
    } else if (windowWidth >= 769 && windowWidth <= 1023){
        contentSetter(currentIndex,2)
    } else {
        contentSetter(currentIndex,4)
    }
}
window.addEventListener('resize', function(event){
    breakpointSetter(currentIndex)
});
breakpointSetter(currentIndex)
</script>