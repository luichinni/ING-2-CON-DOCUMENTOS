import "../HarryStyles/Publicaciones.css"
import React from "react";
import { Link } from "react-router-dom";

const publicacion = (props) => {

    const handleDetalleClick = () => {
        localStorage.setItem("publicacion", JSON.stringify(props));
      };

    return  <fieldset className="publicacion">
                <div className="div">
                    <br/>
                    <img className="img" src={`data: ${props.tipo_imagen};base64,${props.imagen}`} alt="imagen no encontrada"/>
                    <p className="descripcion">
                        {props.nombre}
                        <br/>
                        por: {props.user}
                        <br />
                        centros: {props.centros}
                        <br />
                        descripci&oacute;n:{props.descripcion}
                        <br />
                        categoria: {props.categoria_id}
                    </p>
                </div>
            </fieldset>
}

export default publicacion