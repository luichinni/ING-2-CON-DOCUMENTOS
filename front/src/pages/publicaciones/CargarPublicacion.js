import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import axios from 'axios';
import "../../HarryStyles/estilos.css";

const fileToBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = () => {
            resolve(reader.result);
        };

        reader.onerror = (error) => {
            reject(error);
        };
    });
};

const AgregarPublicacion = () => {
    const [nombre, setNombre] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [fotos, setFotos] = useState([]);
    const [categorias, setCategorias] = useState([]);
    const [categoriaSeleccionada, setCategoriaSeleccionada] = useState('');
    const [centros, setCentros] = useState([]);
    const [centrosSeleccionados, setCentrosSeleccionados] = useState([]);
    const [base64Fotos, setFotosBase64] = useState([]);
    const [errorMessage, setErrorMessage] = useState('');

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDescripcionChange = (e) => setDescripcion(e.target.value);
    const handleFotosChange = async (e) => {
        setFotos([...e.target.files]);

        const base64Array = [];
        for (const file of e.target.files) {
            const base64 = await fileToBase64(file);
            base64Array.push(base64);
        }
        setFotosBase64(base64Array);
    };
    const handleCategoriaChange = (e) => setCategoriaSeleccionada(e.target.value);
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
        } catch (error) {
            setErrorMessage(error.response.data.message);
            console.error('Error:', error);
        }
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const respon = await axios.get(`http://localhost:8000/public/listarCategorias?id=&nombre=`);
                setCategorias(procesarcat(respon.data));
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);

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

    function procesarcat(categorias) {
        let cateCopy = [];
        Object.keys(categorias).forEach(function (clave) {
            if (!isNaN(clave)) {
                cateCopy[clave] = categorias[clave]
            }
        })
        return cateCopy
    }
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
                <input type="text" value={nombre} onChange={handleNombreChange} placeholder="Nombre del producto" required />
                <br /><br />
                <textarea value={descripcion} onChange={handleDescripcionChange} maxLength="255" placeholder="Descripción del producto" required></textarea>
                <br /><br />
                <label>
                    Seleccione las fotos, por lo menos una:
                    <input type="file" accept="image/*" multiple required onChange={handleFotosChange} />
                </label>
                <select id="categoria" onChange={handleCategoriaChange}>
                    <option value="">Seleccione una categoria</option>
                    {categorias.map((categoria) => (
                        <option key={categoria.id} value={categoria.id}>
                            {categoria.nombre}
                        </option>
                    ))}
                </select>
                <br /><br />
                <select id="centro" onChange={handleCentrosChange} multiple>
                    <option value="">Seleccione un centro</option>
                    {centros.map((centro) => (
                        <option key={centro.id} value={centro.id}>
                            {centro.Nombre}
                        </option>
                    ))}
                </select>
                <br /> <br />
                <ButtonSubmit text="Subir producto!" />
                {errorMessage && <p className="error-message">{errorMessage}</p>}
            </form>
        </div>
    );
};

export default AgregarPublicacion;
