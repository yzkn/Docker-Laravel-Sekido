document.addEventListener(
    "DOMContentLoaded",
    () => {
        bubbly({
            blur: 15,
            colorStart: "#BACFFE",
            colorStop: "#FFFEFF",
            radiusFunc: () => 5 + Math.random() * 5,
            angleFunc: () => -Math.PI / 2,
            velocityFunc: () => Math.random() * 3,
            bubbleFunc: () => `hsla(${200 + Math.random() * 50}, 100%, 65%, .1)`,
            bubbles: 50,
        });
    },
    false
);

