import axios from 'axios';
import '../../HarryStyles/centros.css';
import '../../HarryStyles/styles.css'
import { useEffect, useState } from 'react';

const ListarUsuario = () => {
  const [usuarios, setUsuarios] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros, setParametros] = useState({
    userName:"",
    nombre: "",
    apellido:"",
    dni:"",
    mail:"",
    telefono:"",
    rol:""
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams(parametros).toString();
        const url = `http://localhost:8000/public/listarUsuarios?${queryParams}`;
        const response = await axios.get(url);

        if (response.data.length === 0) {
          setError('No hay usuarios disponibles');
          setCentros([]); 
        } else {
          setCentros(procesar(response.data));
        }
      } catch (error) {
        setError('Ocurrió un error al obtener los usuarios.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [parametros]);

  const handleParametrosChange = (newParametros) => {
    setParametros(newParametros);
  };

  function procesar(usuarios) {
    let centroCopy = [];
    Object.keys(usuarios).forEach(function (clave) {
      if (!isNaN(clave)) {
        usuarioCopy[clave] = usuarios[clave]
      }
    })
    return centroCopy
  }

  return (
    <div className='Content'>
      <br /><br /><br /><br /><br /><br /><br />
      <div className='Publi-Div'>
        <FiltroUsuario onFiltroSubmit={handleParametrosChange} />
        {loading ? (
          <h1 className='Cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='SinCentros'>{error}</h1>
        ) : (
          usuarioss.map(usuarios => (
            <User
                userName = {usuarios.userName}
                nombre = {usuarios.nombre}
                apellido = {usuarios.apellido}
                dni = {usuarios.dni}
                mail = {usuarios.mail}
                telefono = {usuarios.telefono}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarUsuarios;