import React from 'react';
import ListarPubliInter from './listarPubliInter';
import '../../HarryStyles/Publicaciones.css';
import '../../HarryStyles/styles.css';


const MisPublis = () => {
  return (
    <div className='dashboard'>
      <br /><br /><br /><br /><br /><br />
      <div className='banner'>
        <h1>Seleccione el producto que desea intercambiar:</h1>
      </div>

      <ListarPubliInter />
    </div>
  );
}

export default MisPublis;