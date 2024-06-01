import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import "../../HarryStyles/estilos.css";
import {TimeClock} from '@mui/x-date-pickers/TimeClock';


const AgregarPublicacion = () => {
    const [nombre, setNombre] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [categoriaSeleccionada, setCategoriaSeleccionada] = useState('');
    const [centros, setCentros] = useState([]);
    const [centrosSeleccionados, setCentrosSeleccionados] = useState([]);
    const [base64Fotos, setFotosBase64] = useState([]);
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

    const navigate = useNavigate();    
    const handleCentrosChange = (e) => {
        const selectedValues = Array.from(e.target.selectedOptions, option => option.value);
        setCentrosSeleccionados(selectedValues);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        base64Fotos.forEach((file, index) => {
            formData.append(`foto${index+1}`, file);
        });
        formData.append('categoria_id', categoriaSeleccionada);
        centrosSeleccionados.forEach((centro, index) => {
            formData.append(`centro${index+1}`, centro);
        });
        formData.append('user',localStorage.getItem('username'));
        formData.append('estado','alta');

        try {
            const response = await axios.post("http://localhost:8000/public/newPublicacion", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
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
                const res = await axios.get(`http://localhost:8000/public/listarCentros?id=&nombre=&direccion=&hora_abre=&hora_cierra=`);
                setCentros(procesarcen(res.data));
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);
    function procesarcen(centros) {
        let cenCopy = [];
        Object.keys(centros).forEach(function (clave) {
            if (!isNaN(clave)) {
                cenCopy[clave] = centros[clave]
            }
        })
        return cenCopy
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
                {/*
                //
                //          NO TIENE FUNCIONALIDAD, ESTA HARDCODEADO
                //
                */}

                <TimeClock />

                <select id="Horario">
                    <option value="">Seleccione un horario</option>
                    <option key="10:00" value="10:00">10:00</option>
                    <option key="10:30" value="10:30">10:30</option>
                    <option key="11:00" value="11:00">11:00</option>
                    <option key="11:30" value="11:30">11:30</option>
                    <option key="12:00" value="12:00">12:00</option>
                    <option key="12:30" value="12:30">12:30</option>
                    <option key="13:00" value="13:00">13:00</option>
                    <option key="13:30" value="13:30">13:30</option>
                    <option key="14:00" value="14:00">14:00</option>
                    <option key="14:30" value="14:30">14:30</option>
                    <option key="15:00" value="15:00">15:00</option>
                    <option key="15:30" value="15:30">15:30</option>
                    <option key="16:00" value="16:00">16:00</option>
                    <option key="16:30" value="16:30">16:30</option>
                    <option key="17:00" value="17:00">17:00</option>
                    <option key="17:30" value="17:30">17:30</option>
                    <option key="18:00" value="18:00">18:00</option>
                    <option key="18:30" value="18:30">18:30</option>
                    <option key="19:00" value="19:00">19:00</option>
                    <option key="19:30" value="19:30">19:30</option>
                    <option key="20:00" value="20:00">20:00</option>
                </select>           
                <ButtonSubmit text="Ofrecer Intercambio" />
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </div>
    );
};

export default AgregarPublicacion;
