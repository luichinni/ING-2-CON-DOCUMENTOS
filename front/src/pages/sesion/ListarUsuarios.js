import axios from 'axios';
import '../../HarryStyles/centros.css';
import '../../HarryStyles/styles.css'
import { useEffect, useState } from 'react';
import FiltroUsuario from '../../components/FiltroUsuario';
import User from '../../components/User'

const ListarUsuario = () => {
  const [usuarios, setUsuarios] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros, setParametros] = useState({
    username:"",
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
          setUsuarios([]); 
        } else {
          setUsuarios(procesar(response.data));
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
    let usuarioCopy = [];
    Object.keys(usuarios).forEach(function (clave) {
      if (!isNaN(clave)) {
        usuarioCopy[clave] = usuarios[clave]
      }
    })
    console.log(usuarioCopy)
    return usuarioCopy
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
          usuarios.map(usuarios => (
            <User
                key = {usuarios.username}
                username = {usuarios.username}
                nombre = {usuarios.nombre}
                apellido = {usuarios.apellido}
                dni = {usuarios.dni}
                mail = {usuarios.mail}
                telefono = {usuarios.telefono}
                rol = {usuarios.rol}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarUsuario;