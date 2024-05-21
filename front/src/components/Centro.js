import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";

const Centro = (props) => {
        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            {props.key}
                            _ {props.nombre}
                        </p>
                        <p className="informacion">
                            <br />
                            direccion: {props.direccion}
                            <br />
                        </p>
                        <p className="info-Dezplazable">
                            hora de apertura: {props.hora_abre}
                            <br />
                            hora de cierre: {props.hora_cierra}
                            <br />
                            <boton className="boton_editar">
                                Editar
                            </boton>
                            <boton className="boton_eliminar">
                                Eliminar
                            </boton>
                            <br />
                        </p>
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
                            categoria: {props.categoria}
                        </p>
                    </div>
                </fieldset>

}

export default Centro