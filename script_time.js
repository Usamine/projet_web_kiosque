document.addEventListener("DOMContentLoaded", () => {
    const currentTimeElement = document.getElementById("currentTime");

    function updateTime() {
        const now = new Date();
        const options = { timeZone: "Africa/Tunis", hour: "2-digit", minute: "2-digit", second: "2-digit" };
        const timeString = now.toLocaleTimeString("fr-FR", options);
        currentTimeElement.textContent = `Heure actuelle : ${timeString}`;
    }

    // Mettre à jour l'heure toutes les secondes
    updateTime();
    setInterval(updateTime, 1000);
});