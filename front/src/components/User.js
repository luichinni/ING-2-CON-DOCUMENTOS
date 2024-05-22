import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import React from "react";

const User = (props) => {
        return  <fieldset className="centro-fila">
                        <p>
                            Nombre de usuario: {props.userName}
                            <br />
                            {props.nombre} {props.apellido} {props.dni} 
                            <br />
                            {props.mail} {props.telefono}
                            <br />
                        </p>
                </fieldset>

}

export default User