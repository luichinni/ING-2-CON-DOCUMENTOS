import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";
import {buttonEliminar} from "./buttonEliminar" 

const Centro = (props) => {
        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            categoria {props.Id}:  {props.Nombre}
                        </p>
                        <button className="boton_editar">
                            Editar
                        </button>
                        <buttonEliminar id={props.id} text={"Eliminar"}/>
                        <br/>
                    </div>
                </fieldset>

}

export default Centro