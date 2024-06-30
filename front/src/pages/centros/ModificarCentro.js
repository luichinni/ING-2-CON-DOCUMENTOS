import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from "react-router-dom";
import axios from 'axios';
import "../../HarryStyles/Intercambios.css"
import "../../HarryStyles/estilos.css";

// idpubli1(publico), idpubli2(oferto), horario, centro

const ModificarCentro = (props) => {
    const navigate = useNavigate();
    const [nombre, setNombre] = useState('');
	const [direccion, setDireccion] = useState('');
	const [hora_abre,setHora_abre] = useState('');
	const [hora_cierra,setHora_cierra] = useState('');
	const [myError, setMyError] = useState(false);
	const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDireccionChange = (e) => setDireccion(e.target.value);
	const handleHora_AbreChange = (e) => setHora_abre (e.target.value);
	const handleHora_CierraChange = (e) => setHora_cierra (e.target.value);   
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        let cambiaFecha = false;

        const formData = new FormData();
        formData.append(`id`,interId);
        formData.append(`userMod`,localStorage.getItem('username'));
        if (cambiaFecha || centroSeleccionado!=""){
            if(cambiaFecha) formData.append(`sethorario`, horarioEnFormato)
            if(centroSeleccionado!="" && centroSeleccionado.id != centroActual.id) formData.append(`setcentro`, centroSeleccionado)

            try {
                console.log(`formData: ${formData}`)
                const response = await axios.put(`http://localhost:8000/public/updateCentro`, formData,
                    {
                        headers: {
                            "Content-Type": "application/json",
                        },
                    })
                console.log('Success:', response);
                navigate("../Centros");
                window.location.reload();
            } catch (error) {
                setMyError(true);
                setMsgError(error.response.data.Mensaje);
            }
        }else{
            navigate("../Centros");
        }
        
    };

    useEffect(() => {
        const fetchData = async () => {
            try { 
                console.log("PUBLI " + publiId);
                console.log("ID " + interId);
                let nuevoArr = [];
                response.data[0].centros.forEach((centro)=> nuevoArr.push(centro));
                

                url = `http://localhost:8000/public/listarIntercambios?id=${interId}&token=${localStorage.getItem('token')}`;
                response = await axios.get(url);

                console.log(response.data[0]);
                let fecha = response.data[0].horario.split(' ')[0];
                setFechaConst(fecha);
                let hora = response.data[0].horario.split(' ')[1];
                console.log('hora -> '+hora);
                setFechaActual(fecha);
                setHorarioActual(hora);
                setCentroActual(response.data[0].centro);
                setHorario(hora.split(':')[0]+':'+hora.split(':')[1]);
                setCentros(nuevoArr);
                
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);

    return (
        <div>
            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            <form onSubmit={handleSubmit}>
                <label> Modificar Centro! No es necesario completar los datos no se necesitan modificar!</label>
                <label>
					Ingrese el  nuevo nombre del centro: <br/>
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<label>
					Ingrese la nueva dirección del centro: <br/>
					<input type="text" value={direccion} onChange={handleDireccionChange} required />
				</label>
				<br />
                <label>
					Ingrese el nuevo horario de apertura del centro: <br/>
					<input type="text" value={hora_abre} onChange={handleHora_AbreChange} required />
				</label>
				<br />
                <label>
					Ingrese el nuevo horario de cierre del centro: <br/>
					<input type="text" value={hora_cierra} onChange={handleHora_CierraChange} required />
				</label>
				<br />

                <ButtonSubmit text="Modificar centro!" /> 
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </div>
    );
};

export default ModificarCentro;
