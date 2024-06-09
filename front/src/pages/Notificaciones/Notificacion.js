import "../../HarryStyles/Notificaciones.css";
import React from "react";
import { Link } from "react-router-dom";

const Notificacion = (props) => {
    return (
        <fieldset className="notificacion">
            <div className="notificacion-info">
                <p className="descripcion">
                    {props.texto}
                </p>
                {(props.url)&&
                <Link
                to={props.url}
                >
                <button className="detalle-Noti">Ver Detalle</button>
                </Link>
                }
                <small className="fecha">{props.fecha}</small>
            </div>
        </fieldset>
    );
};

export default Notificacion;
