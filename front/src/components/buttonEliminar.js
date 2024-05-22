import "../HarryStyles/styles.css";
import React from "react";
import {Link} from "react-router-dom";

const BotonEliminar = ({id, text}) => {
    return  <Link to={"/deleteCategoria/" + id} className="botonEliminar"> {text} </Link>
}
export default BotonEliminar;