import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import "../../HarryStyles/Intercambios.css"
import "../../HarryStyles/estilos.css";

// idpubli1(publico), idpubli2(oferto), horario, centro

const InterCentroHorario = (props) => {
    const [centros, setCentros] = useState([]);
    const [centroSeleccionado, setCentroSeleccionado] = useState("");
    const [horario, setHorario] = useState ("");
    const [dia, setDia] = useState ("");
    const [mes, setMes] = useState ("");
    const [anio, setAnio] = useState ("");
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const dias = Array.from({ length: 31 }, (_, i) => i + 1);
    const meses = [
        { value: 1, nombre: "Enero" },
        { value: 2, nombre: "Febrero" },
        { value: 3, nombre: "Marzo" },
        { value: 4, nombre: "Abril" },
        { value: 5, nombre: "Mayo" },
        { value: 6, nombre: "Junio" },
        { value: 7, nombre: "Julio" },
        { value: 8, nombre: "Agosto" },
        { value: 9, nombre: "Septiembre" },
        { value: 10, nombre: "Octubre" },
        { value: 11, nombre: "Noviembre" },
        { value: 12, nombre: "Diciembre" }
    ];
    const anios = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() + i);
    const horarios = [
        "00:00","00:30","01:00","01:30","02:00","02:30","03:00","03:30","04:00","04:30","05:00","05:30","06:00","06:30",
        "07:00","07:30","08:00","08:30","09:00","09:30","10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", 
        "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30",
        "21:00", "21:30","22:00", "22:30","23:00", "23:30"];

    const id_publi1 = localStorage.getItem("publicacionOferta");

    const navigate = useNavigate();    
    const handleCentrosChange = (e) => {
        setCentroSeleccionado(e.target.value);
        console.log(`centro seleccionado:${centroSeleccionado}`)
    };

    const handleHorarioChange = (e) =>{
        setHorario(e.target.value);
    }
    const handleDiaChange = (e) =>{
        setDia(e.target.value);
    }
    const handleMesChange = (e) =>{
        setMes(e.target.value);
    }
    const handleAnioChange = (e) =>{
        setAnio(e.target.value);
    }
    const handleSubmit = async (e) => {
        e.preventDefault();

        if(!dia){
            setMyError(true);
            setMsgError("Debe seleccionar un Dia.");
            return;
        } else if(!mes){
            setMyError(true);
            setMsgError("Debe seleccionar un mes.");
            return;
        } else if (!anio) {
            setMyError(true);
            setMsgError("Debe seleccionar un anio.");
            return;
        } else if(!horario){
            setMyError(true);
            setMsgError("Debe seleccionar un horario.");
            return;
        }
        const horarioEnFormato = `${anio}-${mes.padStart(2,'0')}-${dia.padStart(2,'0')} ${horario}`

        const fechaSeleccionada = new Date(`${anio}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}T${horario}:00`);
        const fechaActual = new Date();

        if (fechaSeleccionada <= fechaActual) {
            setMyError(true);
            setMsgError("La fecha y hora deben ser posteriores a la fecha y hora actuales.");
            return;
        }

        const formData = new FormData();
        formData.append(`id`, props.idM)
        formData.append(`sethorario`, horarioEnFormato)
        formData.append(`setcentro`, centroSeleccionado)

        try {
            console.log(`formData: ${formData}`)
            const response = await axios.post(`http://localhost:8000/public/updateIntercambio`, formData,
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
                const publicacionGuardada = localStorage.getItem("publica");
                console.log(localStorage.getItem("publi1"))
                console.log(`Datos sin procesar del localStorage: ${publicacionGuardada}`);

                const publicacionObj = JSON.parse(publicacionGuardada);
                console.log(`Datos parseados:`, publicacionObj);

                let nuevoArr = [];
                publicacionObj.centros.forEach((centro)=> nuevoArr.push(centro));
                setCentros(publicacionObj.centros);
                console.log(`centros: ${centros}`)
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);
    const datosCentro = centros.find(centro => centro.id == centroSeleccionado)
    
    const extractTime = (time) => time.slice(0, 5);

    const horariosDisponibles = datosCentro 
    ? horarios.filter(hora => {
        const horaCentroAbre = extractTime(datosCentro.hora_abre);
        const horaCentroCierra = extractTime(datosCentro.hora_cierra);
        return hora >= horaCentroAbre && hora <= horaCentroCierra;
        })
    : [];

    return (
        <div>
            <br /><br /><br /><br /><br /><br />
            <form onSubmit={handleSubmit}>
                <select id="centro" onChange={handleCentrosChange}>
                    <option value="">{props.centroM}</option>
                    {centros.map((centro) => (
                        <option key={centro.id} value={centro.id}>
                            {centro.Nombre}
                        </option>
                    ))}
                </select>
                <br /><br />
                {(centroSeleccionado != "") && (
                <>
                <select id="Horario" value={horario} onChange={handleHorarioChange}>
                    <option value="">Seleccione un horario</option>
                        {horariosDisponibles.map(hora => (
                        <option key={hora} value={hora}>{hora}</option>
                    ))}
                </select>
                <br/><br/>
                <div className="fecha-container">
                    <label>Seleccione una fecha:</label>
                    <div className="fecha-selectores">
                        <select id="dia" value={dia} onChange={handleDiaChange}>
                            <option value="">Día</option>
                            {dias.map(di => (
                            <option key={di} value={di}>{di}</option>
                            ))}
                        </select>
                        <select id="mes" value={mes} onChange={handleMesChange}>
                            <option value="">Mes</option>
                            {meses.map(me => (
                            <option key={me.value} value={me.value}>{me.nombre}</option>
                            ))}
                        </select>
                        <select id="anio" value={anio} onChange={handleAnioChange}>
                            <option value="">Año</option>
                            {anios.map(ani => (
                            <option key={ani} value={ani}>{ani}</option>
                            ))}
                        </select>
                    </div>
                </div>
                <br/><br/>
                <ButtonSubmit text="Ofrecer Intercambio" /> 
                </>
                )}
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </div>
    );
};

export default InterCentroHorario;
