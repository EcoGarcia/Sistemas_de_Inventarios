<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    .wrapper {
        flex: 1;
    }

    .footer-section {
    background-color: gray;
    color: #AAB7B8;
    padding: 20px 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    text-align: right; /* Add this line to move the text to the right */
}

    .container {
        width: 80%;
        margin: 0 auto;
    }

    .smaller-logo {
        max-width: 200px;
        height: auto;
    }

    .footer-text {  
        font-size: 16px;
        text-align: right; /* Add this line to move the text to the right */
        color: white;
    }

    .social-icons {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .social-icons li {
        display: inline-block;
        margin-right: 10px;
    }

    .social-icons a {
        
        text-decoration: none;
        font-size: 18px;
        display: inline-block;
        margin-right: 10px;
    }
</style>

<section class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <a href="#inicio">
                    <!-- <img src="tu-logo.png" alt="Logo de tu sitio" class="smaller-logo"> -->
                </a>
            </div>
            <div class="col-md-9 col-sm-9">
                <p class="footer-text">Autor: García Medina Luis Florentino</a></p>
                </ul>
            </div>
        </div>
    </div>
</section>
