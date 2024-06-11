import axios from 'axios';
import {Link} from 'react-router-dom';
import Centro from '../../components/Centro';
import FiltroCentro from '../../components/FiltroCentro';
import '../../HarryStyles/centros.css';
import '../../HarryStyles/styles.css'
import { useEffect, useState } from 'react';

const ListarCentro = () => {
  const [centros, setCentros] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros, setParametros] = useState({
    id: "",
    nombre: "",
    direccion: "",
    hora_abre: "",
    hora_cierra: ""
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams(parametros).toString();
        const url = `http://localhost:8000/public/listarCentros?${queryParams}`;
        const response = await axios.get(url);

        if (response.data.length === 0) {
          setError('No hay centros disponibles');
          setCentros([]); 
        } else {
          setCentros(procesar(response.data));
        }
      } catch (error) {
        setError('Ocurrió un error al obtener los centros.');
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

  function procesar(centros) {
    let centroCopy = [];
    Object.keys(centros).forEach(function (clave) {
      if (!isNaN(clave)) {
        centroCopy[clave] = centros[clave]
      }
    })
    return centroCopy
  }

  return (
    <div className='Content'>
      <br /><br /><br /><br /><br /><br /><br />
      <div className='Publi-Div'>
        <FiltroCentro onFiltroSubmit={handleParametrosChange} />
        <br />
        {(localStorage.getItem('token') == 'tokenAdmin')?(
        <>
          <div className='botonAgregar'>
              <Link 
                to="/AgregarCentro"
                className="agregarBoton"> 
                Agregar Centro
              </Link>
          </div>
        </>
        ):(<></>)}
        {loading ? (
          <h1 className='Cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='SinCentros'>{error}</h1>
        ) : (
          centros.map(centro => (
            <Centro
                Id = {centro.id}
                Nombre = {centro.Nombre}
                direccion = {centro.direccion}
                hora_abre = {centro.hora_abre}
                hora_cierra = {centro.hora_cierra}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarCentro;