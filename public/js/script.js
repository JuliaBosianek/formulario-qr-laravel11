import { QRCode } from "../lib/qrcode.js";

const codigoQR = document.querySelector("#codigo-qr");      //lo saca solo si se validó bien
const resultadoQR = document.querySelector("#resultado-qr");
const resumenErrores = document.querySelector("#resumen-errores");  //solo existe si hay error

// GENERAR QR
if(codigoQR) {

    //dataset lee los atributos data-* (data-nombre -> dataset.nombre; formulario QR)
    const {nombre, apellidos, dni} = codigoQR.dataset;

    const datos = [`Nombre: ${nombre}`,
                   `Apellidos: ${apellidos}`,
                   `DNI/NIE: ${dni}`].join("\n");   //\n para que no aparezcan los espacios de la sangría

    try{
    // uso de la librería
    const qr = QRCode.encode(datos);

    //convierte qr en SVG
    const svgTexto = qr.toSVG({
        scale: 8,
        title: "Codigo QR con datos personales"     
    });

    codigoQR.innerHTML = svgTexto;

    //svg para meterle estilos inline
    const svg = codigoQR.querySelector("svg");

    if (svg){

        if(!svg.hasAttribute("viewBox")){
            const ancho = svg.getAttribute("width");
            const alto = svg.getAttribute("height");
            svg.setAttribute("viewBox", `0 0 ${ancho} ${alto}`);
        }

        svg.style.width = "250px";
        svg.style.height="auto";
        svg.style.maxWidth="100%";

        //accesibilidad: imagen con nombre(<title>)
        svg.setAttribute("role", "img");
        const titulo = svg.querySelector("title");
        if(titulo){
            titulo.id="titulo-svg-qr";
            svg.setAttribute("aria-labelledby", "titulo-svg-qr");
        }
    }
    }catch(error){
        console.error("Error al generar el código QR:", error);
        codigoQR.textContent="No se ha podido generar el código QR.";
    }
}

//FOCO DESPUES DE RECARGAR LA PÁGINA
if(resultadoQR){
    resultadoQR.focus();
}else if(resumenErrores){
    resumenErrores.focus();
}
