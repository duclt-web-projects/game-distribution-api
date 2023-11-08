const maxWidth = "width: 100%; height: auto; max-height:100%";
const maxHeight = "height: 100%; width: auto; max-width:100%";
const fullscreen = "height: 100%; width: 100%;";

var canvas;
var gameRatio = 1;

function resizeGame() {
    const unityContainer = document.querySelector("#unity-container");

    if (unityContainer) {
        unityContainer.style.cssText = "height:100%; width:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;"
    }

    if (gameRatio > 1) {
        canvas.style.cssText = maxWidth;
    } else {
        canvas.style.cssText = maxHeight;
    }
}

window.onload = function () {
    document.body.style.cssText =
        "height: 100vh; overflow: hidden; margin: 0; display: flex; justify-content: center; align-items: center";
    canvas = document.querySelector('#unity-canvas, #screen, canvas ');
    gameRatio = (canvas.offsetWidth ?? 1) / (canvas.offsetHeight ?? 1);
    resizeGame();
    document.getElementById("unity-footer")?.remove();
};

window.onresize = function () {
    resizeGame(canvas);
};
