import "../HarryStyles/Publicaciones.css";
import React from "react";
import { Link } from "react-router-dom";

const Publicacion = (props) => {
    const handleDetalleClick = () => {
        console.log("AL APRETAR EL BOTON" + JSON.stringify(props.centros));
        localStorage.setItem("publicacion", JSON.stringify(props));
        localStorage.setItem("publicacionOferta", JSON.stringify(props.id));
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
                <br/>
                <Link to={`/PubliDetalle/${props.id}`} onClick={handleDetalleClick}>
                    <button className="detalle-button">Ver Detalle</button>
                </Link>
            </div>
        </fieldset>
    );
};

export default Publicacion;
