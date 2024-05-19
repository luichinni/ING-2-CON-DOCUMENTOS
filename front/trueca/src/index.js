import React from 'react'
import ReactDom from 'react-dom/client'
import { Header } from './componets/Header.js'
import { ListarPublis } from './ListarPublis'

const root = ReactDom.createRoot(document.getElementById('root'))
root.render(inicio())
function inicio() {
    return <>
    <Header />
    <ListarPublis />
    </>
}