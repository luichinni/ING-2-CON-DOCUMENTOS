import "../HarryStyles/Intercambios.css";
import React from "react";
import { Link } from "react-router-dom";

const Intercambio = (props) => {

    return (
        <fieldset className="publicacion">
            <div className="publicacion-img">
                <img className="img" src={props.imagen} alt="imagen no encontrada" />
            </div>
            <div className="publicacion-info">
                <p className="nombre">{props.nombre}</p>
                <p className="descripcion">
                    Descripción: {props.descripcion}
                    <br />
                    Categoría: {props.categoria_id}
                    <br />
                    Por: {props.user}
                </p>
            </div>
        </fieldset>
    );
};

export default Intercambio;
