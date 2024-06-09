import "../../HarryStyles/Notificaciones.css";
import React from "react";

const Notificacion = (props) => {
    return (
        <fieldset className="notificacion">
            <div className="notificacion-info">
                <p className="descripcion">
                    {props.texto}
                </p>
                <small className="fecha">{props.fecha}</small>
            </div>
        </fieldset>
    );
};

export default Notificacion;
