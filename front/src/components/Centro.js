import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React, { useState } from "react";
import {Link} from "react-router-dom";

const Centro = (props) => {
        const [isExpanded, setIsExpanded]= useState(false);

        const handleToggle =()=>{
            setIsExpanded(!isExpanded);
        }


        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            centro {props.Id}:  {props.nombre}
                            <button onClick={handleToggle} className="toggle-button">
                                {isExpanded ? "ocultar Detalles" : "Mostrar Detalles" }
                            </button>
                        </p>
                        {isExpanded && (
                        <div className="detalleUsuario">
                        <p className="informacion">
                            direccion: {props.direccion}
                            <br/>
                            hora de apertura: {props.hora_abre}
                            <br/>
                            hora de cierre: {props.hora_cierra}
                            <br /><br />
                            {(localStorage.getItem('token') == 'tokenAdmin')?(
                            <>
                                <button className="boton_editar">
                                    Editar
                                </button>
                                <Link to={"/deleteCentro/" + props.Id} className="botonEliminar"> Eliminar </Link>
                                <br />
                            </>
                            ):(<></>)}
                        </p>
                        </div>
                        )}
                    </div>
                </fieldset>

}

export default Centro