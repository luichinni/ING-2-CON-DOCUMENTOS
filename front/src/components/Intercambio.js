import "../HarryStyles/Intercambios.css";
import React from "react";

const Intercambio = ({ publicacion1, publicacion2, centro, horario, estado, fecha_propuesta }) => {
    return (
        <li className="intercambio-item">
            <div className="intercambio-content">
                <p><strong>Publicación 1:</strong> {publicacion1}</p>
                <p><strong>Publicación 2:</strong> {publicacion2}</p>
                <p><strong>Centro:</strong> {centro}</p>
                <p><strong>Horario:</strong> {horario}</p>
                <p><strong>Estado:</strong> {estado}</p>
     
            </div>
        </li>
    );
};

export default Intercambio;
