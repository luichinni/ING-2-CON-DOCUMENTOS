import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";
import {Link} from "react-router-dom";

const Categoria = (props) => {
        return  <fieldset className="centro-fila">
                    <div className="div">
                        <p className="nombre">
                            categoria {props.Id}:  {props.Nombre}
                        </p>
                        <button className="boton_editar">
                            Editar
                        </button>
                        <Link to={"/deleteCategoria/" + props.Id} className="botonEliminar"> Eliminar </Link>
                        <br/>
                    </div>
                </fieldset>

}

export default Categoria