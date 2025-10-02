document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.service-card');
    cards.forEach(card => {
        card.style.transition = 'transform 0.15s ease';

        card.addEventListener('click', function () {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.parallax-bg'); 
        if (parallax) {
            parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
});

window.handleBuyTicket = function () {
    console.log('Buy Ticket clicked');
    window.location.href = '/input-telephone'; 
}

window.handlePrintPackage = function () {
    console.log('Print Package clicked');
    window.location.href = '/input-package';
}

window.handlePrintMemberTicket = function () {
    console.log('Print Member Ticket clicked');
    window.location.href = '/input-member';
}

window.handlePrintTrainerTicket = function () {
    console.log('Print Trainer Ticket clicked');
    window.location.href = '/input-coach';
}
