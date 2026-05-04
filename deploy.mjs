import { NodeSSH } from 'node-ssh'
import { execSync } from 'child_process'
import path from 'path'
import { fileURLToPath } from 'url'
import fs from 'fs'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

const config = {
  host: '47.113.110.111',
  username: 'root',
  password: 'Hatumi@2026',
  remoteDir: '/www/wwwroot/htm.benhu.co',
}

async function deploy() {
  const ssh = new NodeSSH()

  console.log('===== 开始部署 =====\n')

  // 1. 构建 admin
  console.log('>>> 步骤1: 构建 admin...')
  execSync('npm run build', { cwd: path.join(__dirname, 'admin'), stdio: 'inherit' })
  console.log('admin 构建完成\n')

  // 2. 本地打包 server 为 tar.gz
  console.log('>>> 步骤2: 打包 server...')
  const archiveName = 'deploy.tar.gz'
  const archivePath = path.join(__dirname, archiveName)
  execSync(`tar -czf ${archiveName} --exclude='.git' -C server .`, { cwd: __dirname, stdio: 'inherit' })
  const sizeMB = (fs.statSync(archivePath).size / 1024 / 1024).toFixed(1)
  console.log(`打包完成，大小: ${sizeMB}MB\n`)

  // 3. 连接服务器
  console.log('>>> 步骤3: 连接服务器...')
  await ssh.connect({
    host: config.host,
    username: config.username,
    password: config.password,
    tryKeyboard: true,
    onKeyboardInteractive: (name, instructions, instructionsLang, prompts, finish) => {
      if (prompts.length > 0 && prompts[0].prompt.toLowerCase().includes('password')) {
        finish([config.password])
      }
    },
  })
  console.log('已连接到服务器\n')

  // 4. 上传压缩包
  console.log('>>> 步骤4: 上传压缩包...')
  await ssh.putFile(archivePath, `${config.remoteDir}/${archiveName}`)
  console.log('上传完成\n')

  // 5. 远程解压
  console.log('>>> 步骤5: 解压并清理...')
  await ssh.execCommand(`cd ${config.remoteDir} && tar -xzf ${archiveName} && rm -f ${archiveName}`)
  console.log('解压完成\n')

  // 6. 设置权限
  console.log('>>> 步骤6: 设置权限...')
  await ssh.execCommand(`chmod -R 755 ${config.remoteDir}/runtime`)
  console.log('权限设置完成\n')

  ssh.dispose()

  // 清理本地压缩包
  fs.unlinkSync(archivePath)

  console.log('===== 部署完成 =====')
}

deploy().catch((err) => {
  console.error('部署失败:', err.message)
  process.exit(1)
})
