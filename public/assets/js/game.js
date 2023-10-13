const maxWidth = "width: 100%; height: auto;";
const maxHeight = "height: 100%; width: auto;";

function resizeGame() {
    setCss('#unity-canvas');
    setCss('canvas');
    setCss('#screen');
    setCss('#unity-container');
    const unityContainer = document.querySelector("#unity-container");

    if (unityContainer) {
        unityContainer.style.cssText = "height:100%; width:100%;"
    }
}

function setCss(cssName) {
    const canvas = document.querySelector(cssName);

    if (!canvas) return;

    const screenRatio = document.documentElement.clientWidth / document.documentElement.clientHeight;
    const gameRatio = (canvas.offsetWidth ?? 1) / (canvas.offsetHeight ?? 1);

    canvas.style.cssText = screenRatio > gameRatio ? maxHeight : maxWidth;
}

window.onload = function () {
    document.body.style.cssText =
        "height: 100vh; overflow: hidden; margin: 0; display: flex; justify-content: center; align-items: center";
    resizeGame();
};

window.onresize = function () {
    resizeGame();
};
