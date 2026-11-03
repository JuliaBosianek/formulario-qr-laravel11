import { QRCode } from "../lib/qrcode.js";

const formulario = document.querySelector("#formulario-qr");
const codigoQR = document.querySelector("#codigo-qr");
const mensaje = document.querySelector("#mensaje");
const textoDatos = document.querySelector("#texto-datos");
const resultadoQR = document.querySelector("#resultado-qr");

formulario.addEventListener("submit",(evento)=>{
    evento.preventDefault();                    //cancela comportamiento del form para no enviar el formulario ni recargar la pagina; datos procesados directamente con js

    const nombre = document.querySelector("#nombre").value;
    const apellidos = document.querySelector("#apellidos").value;
    const dni = document.querySelector("#dni").value;

    const datos = `Nombre: ${nombre}
                   Apellidos: ${apellidos}
                   DNI: ${dni}`;

    // uso de la librería
    const qr = QRCode.encode(datos);

    //convierte qr en SVG
    const svg = qr.toSVG({
        scale: 8,
        title: "Codigo QR con datos personales"     
    });

    codigoQR.innerHTML = svg;

    textoDatos.textContent = datos;
    
    mensaje.textContent = "Codigo QR generado correctamente.";

    resultadoQR.hidden = false;
});