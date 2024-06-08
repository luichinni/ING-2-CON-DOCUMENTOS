import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import "../../HarryStyles/estilos.css";

// idpubli1(publico), idpubli2(oferto), horario, centro

const InterCentroHorario = () => {
    const [centros, setCentros] = useState([]);
    const [centroSeleccionado, setCentroSeleccionado] = useState("");
    const [publicacion2, setPublicacion2] = useState([]);
    const [horario, setHorario] = useState ("");
    const [fecha, setFecha] = useState ("");
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

    const id_publi1 = localStorage.getItem("publiPublico");
    const id_publi2 = localStorage.getItem("publiOferto");

    const navigate = useNavigate();    
    const handleCentrosChange = (e) => {
        setCentroSeleccionado(e.target.value);
    };

    const handleHorarioChange = (e) =>{
        setHorario(e.target.value);
    }
    const handleSubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append(`publicacion1`, id_publi1)
        formData.append(`publicacion2`, id_publi2)
        formData.append(`horario`, horario)
        formData.append(`centro`, centroSeleccionado)
        formData.append(`fecha_propuesta`, centroSeleccionado)

        try {
            const response = await axios.post(`http://localhost:8000/public/newIntercambio`, formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                })
            console.log('Success:', response);
            navigate("../Explorar");
            window.location.reload();
        } catch (error) {
            setMyError(true);
            setMsgError(error.response.data.Mensaje);
        }
    };
    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await axios.get(`http://localhost:8000/public/listarPublicaciones?id=${id_publi1}&token=${localStorage.getItem('token')}`);
                console.log(res.data)
                setCentros(procesarcen(res.data.centro));
                console.log("aca")
                console.log(res)
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);
    function procesarcen(centros) {
        if (!centros) {
            console.log("vacio")
            return [];
        }
        if (Array.isArray(centros)) {
            return centros;
        } else {
        let cenCopy = [];
        Object.keys(centros).forEach(function (clave) {
            if (!isNaN(clave)) {
                cenCopy[clave] = centros[clave]
            }
        })
        return cenCopy}
    }

    return (
        <div>
            <br /><br /><br /><br /><br /><br />
            <form onSubmit={handleSubmit}>
                <select id="centro" onChange={handleCentrosChange}>
                    <option value="">Seleccione un centro</option>
                    {centros.map((centro) => (
                        <option key={centro.id} value={centro.id}>
                            {centro.Nombre}
                        </option>
                    ))}
                </select>
                <br /> <br />
                <select id="Horario" value={horario} onChange={handleHorarioChange}>
                    <option value="">Seleccione un horario</option>
                    {["10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", 
                      "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", 
                      "18:00", "18:30", "19:00", "19:30", "20:00"].map(hora => (
                        <option key={hora} value={hora}>{hora}</option>
                    ))}
                </select>           
                <ButtonSubmit text="Ofrecer Intercambio" />
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </div>
    );
};

export default InterCentroHorario;
