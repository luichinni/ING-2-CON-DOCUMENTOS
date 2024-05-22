import "../HarryStyles/Publicaciones.css";
import React from "react";
import { Link } from "react-router-dom";

const Publicacion = (props) => {
    const handleDetalleClick = () => {
        localStorage.setItem("publicacion", JSON.stringify(props));
    };

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
                <Link to={`/PubliDetalle/${props.id}`} onClick={handleDetalleClick}>
                    <button className="detalle-button">Ver Detalle</button>
                </Link>
            </div>
        </fieldset>
    );
};

export default Publicacion;
