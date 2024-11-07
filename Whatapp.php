<style>
    .whatappContainer {
        margin: 0;
        position: fixed;
        display: flex;
        opacity: 0.6;
        align-items: center;
        justify-content: center;
        right: 15px;
        bottom: 15px;
        width: auto;
        transform: transform 0.3 ease-in-out;
        transition: opacity 0.3 ease-in-out;
        z-index: 3px;
    }

    .whatappContainer img {
        width: 200px;
    }

    .message {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 0;
        opacity: 0;
        background-color: rgba(255, 232, 0, 1);
        border-radius: 10px;
        height: 60px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        transition: width 0.3s ease-in-out;
        margin-right: 10px;
        padding: 5px 10px;
    }

    .message p {
        opacity: 0;
        height: 0;
        width: 0;
        font-size: 18px;
        transition: opacity 1s ease-in;
    }

    .whatappContainer:hover {
        cursor: pointer;
        opacity: 1;
    }

    .whatappContainer:hover .message {
        width: 500px;
        opacity: 1;
    }

    .whatappContainer:hover .message p {
        opacity: 1;
        height: auto;
        width: 100%;
    }

    @media only screen and (max-width:900px) {
        .whatappContainer {
            opacity: 1;
        }

        .whatappContainer img {
            width: 150px;
        }

        .whatappContainer:hover {
            transform: scale(1.1);
        }

        .whatappContainer:hover .message {
            width: 0;
            opacity: 0;
        }
    }
</style>
<a href="https://wa.me/212613131002?text=I'm%20interested%20in%20your%20car%20for%20sale">
    <div class="whatappContainer">
        <div class="message">
            <p>If you have any questions or want to place an order by phone, call the number 0656565622.</p>
        </div>
        <img src="assets/images/WhatsAppButtonGreenLarge.png">
    </div>
</a>