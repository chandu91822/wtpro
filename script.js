function redirectTo(role) {
    window.location.href = role + ".php";
}
function toggleSection(section) {
    document.getElementById("see-classes").style.display = section === "see" ? "block" : "none";
    document.getElementById("book-class").style.display = section === "book" ? "block" : "none";
}

function cancelBooking(bookingId) {
    if (!confirm("Are you sure you want to cancel this booking?")) {
        return;
    }

    fetch("cancel_booking.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "booking_id=" + bookingId
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            document.getElementById("booking-" + bookingId).remove();
            alert("Booking canceled successfully!");
        } else {
            alert("Failed to cancel booking: " + data);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while canceling.");
    });
}


