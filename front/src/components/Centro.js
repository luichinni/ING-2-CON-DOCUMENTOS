import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";

const Centro = (props) => {
        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            {props.key}
                            {props.nombre}
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
                            <button className="boton_editar">
                                Editar
                            </button>
                            <button className="boton_eliminar">
                                Eliminar
                            </button>
                            <br />
                        </p>
                    </div>
                </fieldset>

}

export default Centro