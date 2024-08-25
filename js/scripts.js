function startTutorial(cardId) {
    const card = document.getElementById(cardId);
    const cardTitle = card.querySelector('h3').textContent;

    let steps = [];
    let redirectUrl = '';

    switch (cardTitle) {
        case 'How to Login':
            steps = [
                {
                    intro: "Welcome to the NEMA Customer Care System! Let's guide you through the login process."
                },
                {
                    intro: "You will now be redirected to the login page to complete the process."
                }
            ];

            redirectUrl = './templates/login.php';
            break;

        case 'Where to Check Your License':
            steps = [
                {
                    intro: "You will now be redirected to the license section to apply for your license."
                }
            ];

            redirectUrl = './templates/licensing_portal.php';
            break;

        case 'Calculating Your Total':
            break;

        default:
            console.error('No tutorial found for card:', cardTitle);
            return;
    }

    introJs().setOptions({
        steps: steps,
        nextLabel: 'Next',
        prevLabel: 'Previous',
        skipLabel: 'Skip',
        doneLabel: 'Finish',
        exitOnOverlayClick: false,
        exitOnEsc: false,
    }).oncomplete(function() {
        let nextSteps = [];

        switch (cardTitle) {
            case 'How to Login':
                nextSteps = [
                    {
                        intro: "Enter your username here.",
                        element: '#username',
                        position: 'right'
                    },
                    {
                        intro: "Enter your password here.",
                        element: '#password',
                        position: 'right'
                    },
                    {
                        intro: "Finally, click the 'Login' button to log in to your account.",
                        element: '#loginForm button[type="submit"]',
                        position: 'right'
                    }
                ];
                break;

            case 'Where to Check Your License':
                nextSteps = [
                    {
                        intro: "Click here to start the application process.",
                        element: '#apply-btn',
                        position: 'right'
                    },
                    {
                        intro: "Select license type.",
                        element: '#license_type',
                        position: 'right'
                    },
                    {
                        intro: "Upload required documents.",
                        element: '#documents',
                        position: 'right'
                    },
                    {
                        intro: "Here, you can view the status of all your licenses.",
                        element: '#licenseStatusSection',
                        position: 'right'
                    }
                ];
                break;
        }

        const tutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
        const completeSteps = tutorialSteps.concat(nextSteps);
        localStorage.setItem('tutorialSteps', JSON.stringify(completeSteps));

        window.location.href = redirectUrl;
    }).start();
}
