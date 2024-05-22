import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";

const Centro = (props) => {
        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            categoria {props.Id}:  {props.Nombre}
                        </p>
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