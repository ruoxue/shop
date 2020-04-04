function xuzoom (elem, options) {

  elem.style.position = 'relative'

  var innerImg = elem.querySelector('img'),
    width = innerImg.offsetWidth,
    height = innerImg.offsetHeight
  
  var showBox  = getShowBox()
  var sweepBox = getSweepBox()

  var sweepW = width / 2,
    sweepH = height / 2,
    stepW = sweepW / 2,
    stepH = sweepH / 2

  elem.onmouseenter = function (ev) {

    load(ev.offsetX, ev.offsetY)

  sweepBox.onmousemove = function (e) {
    if (!isMove(e)) {
      return
    }

    var moveX = e.offsetX - stepW
    var moveY = e.offsetY - stepH
    var offsetL = this.offsetLeft
    var offsetT = this.offsetTop
    var toX, toY
    if (moveX > 0 && offsetL < sweepW) {
      toX = Math.min(offsetL + moveX, sweepW) 

    }
    if (moveX < 0 && offsetL > 0) {
      toX = Math.max(offsetL + moveX , 0)
    }
    if(moveY > 0 && offsetT < sweepH) {
      toY = Math.min(offsetT + moveY, sweepH) 
    }
    
    if (moveY < 0 && offsetT > 0) {
      toY = Math.max(offsetT + moveY, 0)
    }
    setStyle(this, {
      left: toX + 'px',
      top: toY + 'px'
    })
    setStyle(showBox, {
      backgroundPositionX: -toX * 2 + 'px',
      backgroundPositionY: -toY * 2 + 'px'
    })

  }
    elem.onmouseleave = function () {
      sweepBox.onmousemove = null
      elem.onmouseleave = null
      unload()
    }
  }

  function unload () {
    showBox.style.display = sweepBox.style.display = 'none'
  }

  function isMove (e) {
    var offsetX = e.offsetX,
        offsetY = e.offsetY,
        offsetLeft = sweepBox.offsetLeft,
        offsetTop = sweepBox.offsetTop

      if (!offsetLeft && !offsetTop) {
        //  左上
        if (offsetX < stepW  && offsetY < stepH ) {
          return false
        }
      }
      if (offsetLeft === sweepW && !offsetTop) {
        //  右上
        if (offsetY < stepH && offsetX > stepW) {
          return false
        }
      }
      if (offsetLeft === sweepW && offsetTop === sweepH) {
        //  右下
        if (offsetX > stepW && offsetY > stepH) {
          return false
        }
      }
      if(!offsetLeft && offsetTop === sweepH) {
        //  左下
        if (offsetX < stepW && offsetY > stepH) {
          return false
        }
      }
      return true;

  }

  function load (x, y) {

    var offsetX = offsetY = 0
    switch ([(x-sweepW) > 0, (y-sweepH) > 0].join(',')) {

      case 'false,true':
        //  左下
        offsetY = sweepH
        break;
      case 'false,false':
        // 左上
        break;
      case 'true,false':
        //  右上
        offsetX = sweepW

      break;
      case 'true,true':
        //  右下
        offsetX = sweepW
        offsetY = sweepH
        break;
    }

    setStyle(sweepBox, {
      left: offsetX + 'px',
      top: offsetY + 'px',
      display: 'block'
    })
    setStyle(showBox, {
      backgroundPositionX: offsetX * 2 + 'px',
      backgroundPositionY: offsetY * 2 + 'px',
      display: 'block'
    })

  }


  function getShowBox () {
    var showBox = document.querySelector('.xu-show-box')
    if (!showBox) {
      showBox = document.createElement('div')
      showBox.className = 'xu-show-box'

      setStyle(showBox, {
        width: (options.offsetWidth || 400) + 'px',
        height: (options.offsetHeight || 400) + 'px',
        position: 'fixed',
        left: elem.offsetLeft + elem.offsetWidth + (options.offsetX || 10) + 'px',
        top: elem.offsetTop + (options.offsetY || 0) + 'px',
        background: 'url(' + elem.getAttribute('data-big-img') + ')',
        display: 'none'
      })

      document.body.appendChild(showBox)
    }

    return showBox
  }

  function getSweepBox () {
    var sweepBox = elem.querySelector('.xu-sweep-box')

    if(!sweepBox) {
      sweepBox = document.createElement('div')
      sweepBox.className = 'xu-sweep-box'
      setStyle(sweepBox, {
        border: '1px solid #44f',
        width: width / 2 - 2+ 'px',
        height: height / 2 - 2 + 'px',
        background: '#ff0',
        opacity: '.4',
        position: 'absolute',
        display: 'none',
        cursor: 'move'
      })
      elem.appendChild(sweepBox)
    }
    
    return sweepBox
  }

  function setStyle(elem, props, value) {
    if (typeof props === 'object') {
      //  传入的对象
      for (var key in props) {
        elem.style[key] = props[key]
      }
    } else {
      elem.style[props] = value
    }
  }
}