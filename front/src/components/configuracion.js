import "../HarryStyles/configuracion.css"
import React, {useState, useEffect} from "react";
import { ButtonSubmit } from "./ButtonSubmit";
import axios from "axios";


const Configuracion = (props) => {
    const [recibeNotis,setRecibe] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
        /* try {
            const respon = await axios.get(`http://localhost:8000/public/listarCategorias?id=&nombre=`);
            setCategorias(procesarcat(respon.data));
            console.log(respon.data);
        } catch (error) {
            console.error(error);
        } */
            
        };
        fetchData();
    }, []);

    const switchHandler = async (e) => {
        const formData = new FormData();
        formData.append('username',localStorage.getItem('username'))
		formData.append('setnotificacion',e.target.value);

        try {
            const response = await axios.put("http://localhost:8000/public/updateUsuario", formData,
                        {
                            headers: {
                                "Content-Type": "application/json",
                            },
                        });
            setRecibe(!recibeNotis)
            
        } catch (error) {
            alert('No fue posible cambiar la preferencia, intente más tarde')
        }
    }

    const estiloRed ={
        "background": "#D02F4C",
        "color": "#FFFFFF"
    }

    const estiloBlue ={
        "background": "#14518B",
        "color": "#FFFFFF"
    }

    return (
        <>
            <br></br>
            <br></br>
            <br></br>
            <br></br>
            <br></br>
            <br></br>
            <br></br>
            <div class="transparent-box">
            {recibeNotis && 
                <button value={false} style={estiloRed} onClick={switchHandler}>Desactivar notificaciones al mail</button>
            }
            {!recibeNotis &&
                <button value={true} style={estiloBlue} onClick={switchHandler}>Activar notificaciones al mail</button>
            }
            </div>
        </>
    )
}

export default Configuracion