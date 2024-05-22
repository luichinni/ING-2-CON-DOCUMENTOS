import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import axios from 'axios';

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

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDescripcionChange = (e) => setDescripcion(e.target.value);
    const handleFotosChange = async (e) => {
		setFotos([...e.target.files]);

		const base64Array = [];
		for (const file of e.target.files) {
			const base64 = await fileToBase64(file);
			base64Array.push(base64);
		}
		console.log(base64Array);
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
            console.error('Error:', error);
        }
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const respon = await axios.get(`http://localhost:8000/public/listarCategorias?id=&nombre=`);
                setCategorias(procesarcat(respon.data));
                console.log(respon.data);
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
                console.log(res.data);
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
                <label>
                    Nombre del producto:
                    <input type="text" value={nombre} onChange={handleNombreChange} />
                </label>
                <br />
                <label>
                    Descripción del producto:
                    <textarea value={descripcion} onChange={handleDescripcionChange}></textarea>
                </label>
                <br />
                <label>
                    Seleccione las fotos, por lo menos una:
                    <input type="file" accept="image/*" multiple onChange={handleFotosChange} />
                </label>
                <br />
                <label>
                    Categoría:
                    <select id="categoria" onChange={handleCategoriaChange}>
                        <option value="">Categoria</option>
                        {categorias.map((categoria) => (
                            <option key={categoria.id} value={categoria.id}>
                                {categoria.nombre}
                            </option>
                        ))}
                    </select>
                </label>
                <br /><br />
                <label>
                    Centros:
                    <select id="centro" onChange={handleCentrosChange} multiple>
                        <option value="">Centro</option>
                        {centros.map((centro) => (
                            <option key={centro.id} value={centro.id}>
                                {centro.nombre}
                            </option>
                        ))}
                    </select>
                </label>
                <br />
                <ButtonSubmit text="Subir producto!" />
            </form>
        </div>
    );
};

export default AgregarPublicacion;
