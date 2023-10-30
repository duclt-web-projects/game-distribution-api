const maxWidth = "width: 100%; height: auto;";
const maxHeight = "height: 100%; width: auto;";
const fullscreen = "height: 100%; width: 100%;";

var canvas;
var gameRatio = 1;

function resizeGame() {
    const unityContainer = document.querySelector("#unity-container");

    if (unityContainer) {
        unityContainer.style.cssText = "height:100%; width:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;"
    }

    const screenRatio = document.documentElement.clientWidth / document.documentElement.clientHeight;

    console.log(screenRatio, gameRatio)

    if (gameRatio > 1) {
        canvas.style.cssText = fullscreen;
    } else {
        if (screenRatio > 1) {
            canvas.style.cssText = maxHeight;
        } else {
            canvas.style.cssText = fullscreen;
        }
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
